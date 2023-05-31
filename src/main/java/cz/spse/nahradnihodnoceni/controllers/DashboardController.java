package cz.spse.nahradnihodnoceni.controllers;

import java.util.List;

import cz.spse.nahradnihodnoceni.models.DashboardStats;
import cz.spse.nahradnihodnoceni.models.data.Classroom;
import cz.spse.nahradnihodnoceni.models.data.Exam;
import cz.spse.nahradnihodnoceni.models.data.Student;
import cz.spse.nahradnihodnoceni.models.data.Subject;
import cz.spse.nahradnihodnoceni.repositories.ExamRepository;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.MediaType;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.RestController;

@RestController
@RequestMapping("/api/1/dashboard")
public class DashboardController {
    @Autowired
    private ExamRepository examRepository;

    @GetMapping(value = "/stats", produces = MediaType.APPLICATION_JSON_VALUE)
    public DashboardStats dashboard() {
        return new DashboardStats(256, 120, 52, 10, 69, 42, 128);
    }

    @GetMapping(value = "/exams", produces = MediaType.APPLICATION_JSON_VALUE)
    public List<Exam> filteredExams(@RequestParam(required = false) String status, @RequestParam(required = false) String type, @RequestParam(required = false) String successful, @RequestParam(required = false) String mark, @RequestParam(required = false) String form) {

        return examRepository.findByStatusTypeSuccessfulMark(status, type, successful, mark);
    }
}
