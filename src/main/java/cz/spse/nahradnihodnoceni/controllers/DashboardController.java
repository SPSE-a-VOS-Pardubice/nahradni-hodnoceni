package cz.spse.nahradnihodnoceni.controllers;

import com.fasterxml.jackson.core.JsonProcessingException;
import com.fasterxml.jackson.databind.ObjectMapper;
import cz.spse.nahradnihodnoceni.helpers.MapperHelper;
import cz.spse.nahradnihodnoceni.models.DashboardStats;
import cz.spse.nahradnihodnoceni.repositories.ExamRepository;
import org.springframework.data.domain.Example;
import org.springframework.data.domain.PageRequest;
import org.springframework.data.domain.Pageable;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.data.domain.PageRequest;
import org.springframework.data.domain.Sort;
import org.springframework.http.MediaType;
import org.springframework.web.bind.annotation.*;

@RestController
@RequestMapping("/api/1/dashboard")
public class DashboardController {
    @Autowired
    private ExamRepository examRepository;

    private final ObjectMapper mapper = MapperHelper.createMapper();

    @CrossOrigin // TODO only for development
    @GetMapping(value = "/stats", produces = MediaType.APPLICATION_JSON_VALUE)
    public String dashboard() throws JsonProcessingException {
        var entity = new DashboardStats(
                examRepository.findAllNH().size(),
                examRepository.findAllFinishedNH().size(),
                examRepository.findAllOZ().size(),
                examRepository.findAllFinishedOZ().size(),
                examRepository.findAllSuccesedExams().size(),
                examRepository.findAllFailedExams().size(),
                examRepository.findAllUnmarkedExams().size()
            );

        return mapper.writeValueAsString(entity);
    }

    @CrossOrigin // TODO only for development
    @GetMapping(value = "/exams/{page}", produces = MediaType.APPLICATION_JSON_VALUE)
    public String filteredExams(@RequestParam(required = false) Integer status, @RequestParam(required = false) Integer type, @RequestParam(required = false) Integer successful, @RequestParam(required = false) Integer mark, @RequestParam(required = false) Integer form, @PathVariable(name = "page") Integer page) throws JsonProcessingException {

        // TODO check page >= 0

        var pageable = PageRequest.of(page, 2, Sort.unsorted()); // TODO change page size to ~20
        var entity = examRepository.findByStatusTypeSuccessfulMark(status, type, successful, mark, pageable);
        return mapper.writeValueAsString(entity);
    }
}
