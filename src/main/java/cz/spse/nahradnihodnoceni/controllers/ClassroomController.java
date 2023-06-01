package cz.spse.nahradnihodnoceni.controllers;

import cz.spse.nahradnihodnoceni.models.data.Classroom;
import cz.spse.nahradnihodnoceni.repositories.ClassroomRepository;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;

@RestController
@RequestMapping("/api/1/data/classroom")
public class ClassroomController extends DataController<Classroom, ClassroomRepository> {}