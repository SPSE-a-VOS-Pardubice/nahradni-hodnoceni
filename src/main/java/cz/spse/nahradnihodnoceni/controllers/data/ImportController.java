package cz.spse.nahradnihodnoceni.controllers.data;

import com.opencsv.bean.CsvToBean;
import com.opencsv.bean.CsvToBeanBuilder;
import cz.spse.nahradnihodnoceni.models.ImportEntry;
import cz.spse.nahradnihodnoceni.models.data.*;
import cz.spse.nahradnihodnoceni.repositories.*;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.RestController;
import org.springframework.web.multipart.MultipartFile;

import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.io.Reader;
import java.time.LocalDate;
import java.util.List;
import java.util.Set;

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

    @PostMapping("/api/1/upload")
    public List<ImportEntry> uploadCSVFile(@RequestParam("file") MultipartFile file) throws Exception {

        var currentYear = LocalDate.now().getYear();

        // validate file
        if (file.isEmpty()) {
            return null;
        }

        Reader reader = new BufferedReader(new InputStreamReader(file.getInputStream()));

        CsvToBean<ImportEntry> csvToBean = new CsvToBeanBuilder<ImportEntry>(reader)
                .withType(ImportEntry.class)
                .withSeparator(';')
                .withIgnoreLeadingWhiteSpace(true)
                .withIgnoreEmptyLine(true)
                .withSkipLines(1)
                .build();
        List<ImportEntry> entries = csvToBean.parse();

        for (ImportEntry entry: entries) {

            _Class _class = classRepository.find(
                calculateYear(entry.getClassPartialYear(), currentYear),
                entry.getClassLabel()
            );
            if (_class == null) {
                _class = new _Class();
                _class.setYear(calculateYear(entry.getClassPartialYear(), currentYear));
                _class.setLabel(entry.getClassLabel());
                classRepository.save(_class);
            }

            Subject subject = subjectRepository.findByAbbreviation(entry.getSubjectAbbreviation());
            if (subject == null)
                continue;

            Teacher examiner = teacherRepository.findBySurname(entry.getExaminerSurname());
            if (examiner == null)
                continue;

            if (!Set.of(Exam.marks).contains(entry.getMark()))
                continue;

            Student student = studentRepository.findByNameAndClass(
                    entry.getStudentName(),
                    entry.getStudentSurname(),
                    calculateYear(entry.getClassPartialYear(), currentYear),
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
            examRepository.save(exam);
        }

        return entries;
    }

    private int calculateYear(int partialYear, int relativeTo) {
        return Math.max(1, relativeTo - partialYear);
    }
}
