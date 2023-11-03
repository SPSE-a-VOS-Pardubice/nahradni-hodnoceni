package cz.spse.nahradnihodnoceni.controllers;

import com.fasterxml.jackson.core.JsonProcessingException;
import com.fasterxml.jackson.databind.ObjectMapper;
import cz.spse.nahradnihodnoceni.helpers.MapperHelper;
import cz.spse.nahradnihodnoceni.repositories.ExamRepository;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.MediaType;
import org.springframework.web.bind.annotation.*;

@RestController
@RequestMapping("/api/1/dashboard")
public class DashboardController {
    @Autowired
    private ExamRepository repository;

    private final ObjectMapper mapper = MapperHelper.createMapper();

    @CrossOrigin // TODO
    @GetMapping(value = "/exams/{year}/{period}", produces = MediaType.APPLICATION_JSON_VALUE)
    public String filteredExams(@PathVariable int year, @PathVariable String period) throws JsonProcessingException {

        var exams = period.equals("1") ? repository.getForFirstPeriod(year + 1) : repository.getForSecondPeriod(year + 1);

        return mapper.writeValueAsString(exams);
    }

    @CrossOrigin
    @GetMapping(value = "/exams/oldest-year", produces = MediaType.APPLICATION_JSON_VALUE)
    public String oldestYear() {
        var year = repository.getOldestExamYear();
        return year == null ? "null" : String.format("%d", year - 1);
    }
}
