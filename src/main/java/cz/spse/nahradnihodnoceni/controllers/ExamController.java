package cz.spse.nahradnihodnoceni.controllers;

import cz.spse.nahradnihodnoceni.models.data.Exam;
import cz.spse.nahradnihodnoceni.repositories.ExamRepository;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;

@RestController
@RequestMapping("/api/1/data/exam")
public class ExamController extends DataController<Exam, ExamRepository> {}