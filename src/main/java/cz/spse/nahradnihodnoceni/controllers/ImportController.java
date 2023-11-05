package cz.spse.nahradnihodnoceni.controllers;

import com.opencsv.bean.CsvToBean;
import com.opencsv.bean.CsvToBeanBuilder;
import cz.spse.nahradnihodnoceni.models.ImportEntry;
import cz.spse.nahradnihodnoceni.models.data.*;
import cz.spse.nahradnihodnoceni.models.responses.FailedUploadResponse;
import cz.spse.nahradnihodnoceni.repositories.*;
import lombok.AllArgsConstructor;
import lombok.Data;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.RestController;
import org.springframework.web.multipart.MultipartFile;

import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.io.Reader;
import java.util.*;

@RestController
public class ImportController {
    @Autowired
    private _ClassRepository classRepository;
    @Autowired
    private StudentRepository studentRepository;
    @Autowired
    private SubjectRepository subjectRepository;
    @Autowired
    private TeacherRepository teacherRepository;

    @Autowired
    private ExamRepository examRepository;

    private int calculateAbsoluteYear(int partialYear, int relativeTo) {
        return Math.max(1, relativeTo - partialYear);
    }

    @PostMapping("/api/1/upload")
    public FailedUploadResponse uploadCSVFile(@RequestParam("file") MultipartFile file, @RequestParam int year, @RequestParam int period) throws Exception {

        if (period != 1 && period != 2)
            throw new IllegalArgumentException("period must be either 1 or 2");
        if (file.isEmpty())
            return null;

        Reader reader = new BufferedReader(new InputStreamReader(file.getInputStream()));

        CsvToBean<ImportEntry> csvToBean = new CsvToBeanBuilder<ImportEntry>(reader)
                .withType(ImportEntry.class)
                .withSeparator(';')
                .withIgnoreLeadingWhiteSpace(true)
                .withIgnoreEmptyLine(true)
                .withSkipLines(1)
                .build();
        List<ImportEntry> entries = csvToBean.parse();

        Map<String, Subject> subjects = new HashMap<>();
        Map<KnownExaminerDetails, Teacher> teachers = new HashMap<>();

        Set<String> missingSubjects = new HashSet<>();
        Set<KnownExaminerDetails> missingExaminers = new HashSet<>();

        // přednačteme předměty a zkoušející, abychom zkontrolovali, že žádní v DB nechybí
        for (ImportEntry entry: entries) {
            var subjectAbbreviation = entry.getSubjectAbbreviation();
            if (!missingSubjects.contains(subjectAbbreviation) && !subjects.containsKey(subjectAbbreviation)) {
                Subject subject = subjectRepository.findByAbbreviation(subjectAbbreviation);
                if (subject == null)
                    missingSubjects.add(subjectAbbreviation);
                else
                    subjects.put(subjectAbbreviation, subject);
            }

            var examinerName = entry.getExaminerName();
            var examinerSurname = entry.getExaminerSurname();
            var uniqueExaminerKey = new KnownExaminerDetails(examinerName, examinerSurname);
            if (!missingExaminers.contains(uniqueExaminerKey) && !teachers.containsKey(uniqueExaminerKey)) {
                Teacher examiner = teacherRepository.findActiveByNameSurname(examinerName, examinerSurname);
                if (examiner == null)
                    missingExaminers.add(uniqueExaminerKey);
                else
                    teachers.put(uniqueExaminerKey, examiner);
            }
        }

        if (!missingSubjects.isEmpty() || !missingExaminers.isEmpty()) {
            return new FailedUploadResponse(missingSubjects, missingExaminers);
        }

        for (ImportEntry entry: entries) {

            _Class _class = classRepository.find(
                    calculateAbsoluteYear(entry.getClassRelativeYear(), year + 1),
                    entry.getClassLabel()
            );
            if (_class == null) {
                _class = new _Class();
                _class.setYear(calculateAbsoluteYear(entry.getClassRelativeYear(), year + 1));
                _class.setLabel(entry.getClassLabel());
                classRepository.save(_class);
            }

            var subject = subjects.get(entry.getSubjectAbbreviation());
            if (subject == null)
                continue;

            var uniqueExaminerKey = new KnownExaminerDetails(entry.getExaminerName(), entry.getExaminerSurname());
            var examiner = teachers.get(uniqueExaminerKey);
            if (examiner == null)
                continue;

            if (!Set.of(Exam.marks).contains(entry.getMark()))
                continue;

            Student student = studentRepository.findByNameAndClass(
                    entry.getStudentName(),
                    entry.getStudentSurname(),
                    calculateAbsoluteYear(entry.getClassRelativeYear(), year + 1),
                    entry.getClassLabel()
            );
            if (student == null) {
                student = new Student();
                student.set_class(_class);
                student.setName(entry.getStudentName());
                student.setSurname(entry.getStudentSurname());
                studentRepository.save(student);
            }

            Exam exam = new Exam();
            exam.setSubject(subject);
            exam.setExaminer(examiner);
            exam.setStudent(student);
            exam.setOriginalMark(entry.getMark());
            exam.setYear(year);
            exam.setPeriod(period);
            examRepository.save(exam);
        }

        return null;
    }

    @AllArgsConstructor
    @Data
    public static class KnownExaminerDetails {
        private String name;
        private String surname;
    }
}
