package cz.spse.nahradnihodnoceni.controllers.data;

import com.opencsv.bean.CsvToBean;
import com.opencsv.bean.CsvToBeanBuilder;
import cz.spse.nahradnihodnoceni.models.ImportEntry;
import cz.spse.nahradnihodnoceni.models.data.*;
import cz.spse.nahradnihodnoceni.repositories.*;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.data.repository.CrudRepository;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.RestController;
import org.springframework.web.multipart.MultipartFile;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.Reader;
import java.util.List;
import java.util.Map;
import java.util.Set;
import java.util.TreeMap;

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

    @PostMapping("/upload-csv-file")
    public List<ImportEntry> uploadCSVFile(@RequestParam("file") MultipartFile file) throws IOException {

        // validate file
        if (file.isEmpty()) {
            return null;
        }

        Reader reader = new BufferedReader(new InputStreamReader(file.getInputStream()));

        // create csv bean reader
        CsvToBean<ImportEntry> csvToBean = new CsvToBeanBuilder<ImportEntry>(reader)
                .withType(ImportEntry.class)
                .withSeparator(';')
                .withIgnoreLeadingWhiteSpace(true)
                .build();

        // convert `CsvToBean` object to list of users
        List<ImportEntry> entries = csvToBean.parse();

        // load data from DB and mapping them, for easy access
        Map<String, _Class> classes = loadEntities((_Class c) -> c.countYear() + "" + c.getLabel(), classRepository);
        Map<String, Subject> subjects = loadEntities((Subject s) -> s.getAbbreviation(), subjectRepository);
        Map<String, Teacher> teachers = loadEntities((Teacher t) -> t.getSurname(), teacherRepository);
        Map<String, Student> students = loadEntities((Student s) -> String.format("%s%s%s",
                        s.get_class().countYear() + s.get_class().getLabel(),
                        s.getSurname(),
                        s.getName())
                , studentRepository);

        // creating exams
        for (ImportEntry entry: entries) {
            // getting data
            _Class c = classes.get(entry.getTrida());
            Subject subj = subjects.get(entry.getPredmet());
            Teacher t = teachers.get(entry.getZkousejici());

            // validate
                if (c != null && subj != null && t != null && Set.of(Exam.marks).contains(entry.getZnamka())) {
                // searching student
                Student s = students.get(entry.getTrida() + entry.getPrijmeni() + entry.getJmeno());

                Exam e = new Exam();
                // create student if it is necessarily
                if (s == null) {
                    s = new Student();
                    s.set_class(c);
                    s.setName(entry.getJmeno());
                    s.setSurname(entry.getPrijmeni());

                    studentRepository.save(s);
                    students.put(String.format("%s%s%s", c.countYear() + c.getLabel(), s.getSurname(), s.getName()), s);
                }

                // fill exam by data
                e.setSubject(subj);
                e.setExaminer(t);
                e.setStudent(s);
                e.setOriginalMark(entry.getZnamka());

                examRepository.save(e);
            }
        }

        return entries;
    }

    private <E> Map<String, E> loadEntities(KeyExtract<E> keyExtraction, CrudRepository<E, Long> repository) {
        Iterable<E> iterable = repository.findAll();

        Map<String, E> data = new TreeMap<>();
        for (E e : iterable) {
            data.put(keyExtraction.extractKey(e), e);
        }

        return data;
    }

    @FunctionalInterface
    private interface KeyExtract<E> {
         String extractKey(E entity);
    }
}
