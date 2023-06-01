package cz.spse.nahradnihodnoceni.controllers.data;

import cz.spse.nahradnihodnoceni.controllers.DataController;
import cz.spse.nahradnihodnoceni.models.data.Teacher;
import cz.spse.nahradnihodnoceni.repositories.TeacherRepository;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;

@RestController
@RequestMapping("/api/1/data/teacher")
public class TeacherController extends DataController<Teacher, TeacherRepository> {}
