package cz.spse.nahradnihodnoceni.controllers;

import java.util.List;

import cz.spse.nahradnihodnoceni.models.DashboardStats;
import cz.spse.nahradnihodnoceni.models.data.Exam;
import cz.spse.nahradnihodnoceni.repositories.ExamRepository;
import org.springframework.data.domain.PageRequest;
import org.springframework.data.domain.Pageable;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.data.domain.Sort;
import org.springframework.http.MediaType;
import org.springframework.web.bind.annotation.*;

@RestController
@RequestMapping("/api/1/dashboard")
public class DashboardController {
    @Autowired
    private ExamRepository examRepository;

    @CrossOrigin // TODO only for development
    @GetMapping(value = "/stats", produces = MediaType.APPLICATION_JSON_VALUE)
    public DashboardStats dashboard() {
        return new DashboardStats(256, 120, 52, 10, 69, 42, 128);
    }

    @CrossOrigin // TODO only for development
    @GetMapping(value = "/exams/{page}", produces = MediaType.APPLICATION_JSON_VALUE)
    public List<Exam> filteredExams(@RequestParam(required = false) Integer status, @RequestParam(required = false) Integer type, @RequestParam(required = false) Integer successful, @RequestParam(required = false) Integer mark, @RequestParam(required = false) Integer form, @PathVariable(name = "page") Integer page) {

        Pageable pageable = PageRequest.of(page, 20, Sort.unsorted());

        return examRepository.findByStatusTypeSuccessfulMark(status, type, successful, mark, pageable);
    }
}
