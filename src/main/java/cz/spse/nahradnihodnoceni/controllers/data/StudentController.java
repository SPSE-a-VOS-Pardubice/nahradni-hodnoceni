package cz.spse.nahradnihodnoceni.controllers.data;

import cz.spse.nahradnihodnoceni.controllers.DataController;
import cz.spse.nahradnihodnoceni.models.data.Student;
import cz.spse.nahradnihodnoceni.repositories.StudentRepository;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;

@RestController
@RequestMapping("/api/1/data/student")
public class StudentController extends DataController<Student, StudentRepository> {}