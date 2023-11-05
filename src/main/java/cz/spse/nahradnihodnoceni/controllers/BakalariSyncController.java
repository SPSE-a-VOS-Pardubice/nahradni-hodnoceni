package cz.spse.nahradnihodnoceni.controllers;

import com.fasterxml.jackson.annotation.JsonIgnoreProperties;
import com.fasterxml.jackson.core.JsonProcessingException;
import com.fasterxml.jackson.databind.ObjectMapper;
import cz.spse.nahradnihodnoceni.models.data.Subject;
import cz.spse.nahradnihodnoceni.models.data.Teacher;
import cz.spse.nahradnihodnoceni.repositories.SubjectRepository;
import cz.spse.nahradnihodnoceni.repositories.TeacherRepository;
import lombok.AllArgsConstructor;
import lombok.Data;
import org.jsoup.Jsoup;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;

import java.io.IOException;
import java.util.*;
import java.util.stream.Stream;

@RestController
@RequestMapping("/api/1/bakalari")
public class BakalariSyncController {
    private final String TIMETABLE_HOMEPAGE = "https://bakalari.spse.cz/bakaweb/timetable/public/";
    private final String TIMETABLE_PERMANENT_FORMAT = "https://bakalari.spse.cz/bakaweb/Timetable/Public/Permanent/Class/%s";
    private final List<String> SUFFIXES = List.of("MBA");

    private final ObjectMapper objectMapper = new ObjectMapper();

    @Autowired
    private SubjectRepository subjectRepository;

    @Autowired
    private TeacherRepository teacherRepository;

    @PostMapping("/sync")
    public SyncResult sync() throws IOException {
        Set<String> teacherNames = new HashSet<>();
        Map<String, String> subjectMap = new HashMap<>();

        getClassIds().forEach(classId -> {
            try {
                extractDataFromSingleTimetable(classId).forEach(lesson -> {
                    teacherNames.add(lesson.fullTeacherName);
                    subjectMap.put(lesson.subjectAbbreviation, lesson.subject);
                });
            } catch (IOException e) {
                throw new RuntimeException(e);
            }
        });

        int insertedSubjects = 0;
        for (var entry : subjectMap.entrySet()) {
            var subject = subjectRepository.findByAbbreviation(entry.getKey());
            if (subject != null)
                continue;

            subject = Subject.builder()
                    .abbreviation(entry.getKey())
                    .name(entry.getValue())
                    .build();
            subjectRepository.save(subject);
            insertedSubjects++;
        }

        int insertedTeachers = 0;
        for (var teacherName : teacherNames) {
            var components = teacherName.split(" ");
            var prefixes = Arrays.stream(components)
                    .takeWhile(component -> component.endsWith(".") || component.endsWith(".,"))
                    .map(component -> component.endsWith(",") ? component.substring(0, component.length() - 1) : component)
                    .toList();

            var reversedComponentsList = new ArrayList<>(List.of(components));
            Collections.reverse(reversedComponentsList);

            var suffixes = reversedComponentsList.stream()
                    .takeWhile(component -> component.endsWith(".") || SUFFIXES.contains(component))
                    .toList();

            if (prefixes.size() + suffixes.size() + 2 != components.length) {
                System.out.printf("unparsable teacher name: %s\n", teacherName);
                continue;
            }
            var name = components[prefixes.size()];
            var surname = components[prefixes.size() + 1];

            var teacher = teacherRepository.findActiveByNameSurname(name, surname);
            if (teacher != null)
                continue;

            teacher = Teacher.builder()
                    .name(name)
                    .surname(surname)
                    .prefix(String.join(" ", prefixes))
                    .suffix(String.join(" ", suffixes))
                    .build();
            teacherRepository.save(teacher);
            insertedTeachers++;
        }

        return new SyncResult(
                subjectMap.size(),
                insertedSubjects,
                teacherNames.size(),
                insertedTeachers
        );
    }

    private Stream<String> getClassIds() throws IOException {
        var doc = Jsoup.connect(TIMETABLE_HOMEPAGE).get();
        var classEls = doc.select("#selectedClass > option[value]");
        return classEls.stream().map(classEl -> classEl.attr("value"));
    }

    private Stream<Lesson> extractDataFromSingleTimetable(String classId) throws IOException {
        var url = String.format(TIMETABLE_PERMANENT_FORMAT, classId);
        var doc = Jsoup.connect(url).get();

        return doc.select(".day-item-hover").stream()
                .map(lessonEl -> {
                    DetailData json;
                    try {
                        json = objectMapper.readValue(lessonEl.dataset().get("detail"), DetailData.class);
                    } catch (JsonProcessingException e) {
                        throw new RuntimeException(e);
                    }

                    var subject = json.subjecttext.split("\\|")[0].strip();

                    var subjectAbbreviation = lessonEl.select(".middle").first().text()
                            .strip()
                            .replaceFirst("^L: ", "")
                            .replaceFirst("^S: ", "");

                    return new Lesson(json.getTeacher(), subject, subjectAbbreviation);
                });
    }

    @Data
    @JsonIgnoreProperties(ignoreUnknown = true)
    private static class DetailData {
        private String teacher;
        private String subjecttext;
    }

    @AllArgsConstructor
    @Data
    private static class Lesson {
        private String fullTeacherName;
        private String subject;
        private String subjectAbbreviation;
    }

    @AllArgsConstructor
    @Data
    private static class SyncResult {
        private int syncedSubjects;
        private int insertedSubjects;
        private int syncedTeachers;
        private int insertedTeachers;
    }
}
