package cz.spse.nahradnihodnoceni.controllers;

import com.fasterxml.jackson.core.JsonProcessingException;
import com.fasterxml.jackson.databind.ObjectMapper;
import cz.spse.nahradnihodnoceni.helpers.MapperHelper;
import cz.spse.nahradnihodnoceni.repositories.ExamRepository;
import lombok.AllArgsConstructor;
import lombok.Data;
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
    public String filteredExams(@PathVariable int year, @PathVariable int period) throws JsonProcessingException {
        var exams = repository.getForPeriod(year, period);
        return mapper.writeValueAsString(exams);
    }

    @CrossOrigin
    @GetMapping(value = "/exams/period-range", produces = MediaType.APPLICATION_JSON_VALUE)
    public String oldestYear() throws JsonProcessingException {
        var oldestExam = repository.getOldestExam();
        var latestExam = repository.getLatestExam();

        if (oldestExam == null || latestExam == null) {
            return "null";
        }

        var periodRange = new PeriodRange(
                new Period(oldestExam.getYear(), oldestExam.getPeriod()),
                new Period(latestExam.getYear(), latestExam.getPeriod())
        );
        return mapper.writeValueAsString(periodRange);
    }

    @Data
    @AllArgsConstructor
    private static class PeriodRange {
        private Period oldest;
        private Period latest;
    }

    @Data
    @AllArgsConstructor
    private static class Period {
        private int year;
        private int period;
    }
}
