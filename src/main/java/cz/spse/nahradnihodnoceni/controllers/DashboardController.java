package cz.spse.nahradnihodnoceni.controllers;

import com.fasterxml.jackson.core.JsonProcessingException;
import com.fasterxml.jackson.databind.ObjectMapper;
import cz.spse.nahradnihodnoceni.helpers.MapperHelper;
import cz.spse.nahradnihodnoceni.models.DashboardStats;
import cz.spse.nahradnihodnoceni.repositories.ExamRepository;
import cz.spse.nahradnihodnoceni.repositories.ExamRepositoryOther;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.data.domain.PageRequest;
import org.springframework.data.domain.Sort;
import org.springframework.http.MediaType;
import org.springframework.lang.Nullable;
import org.springframework.web.bind.annotation.*;

@RestController
@RequestMapping("/api/1/dashboard")
public class DashboardController {
    @Autowired
    private ExamRepository examRepository;

    @Autowired
    private ExamRepositoryOther examRepositoryOther;

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
    public String filteredExams(@RequestParam(defaultValue = "") String status, @RequestParam(defaultValue = "") String type, @RequestParam(defaultValue = "") String successful, @RequestParam(required = false) String mark, @RequestParam(required = false) String form, @PathVariable(name = "page") Integer page) throws JsonProcessingException {

        // TODO check page >= 0

        var pageable = PageRequest.of(page, 20, Sort.unsorted()); // TODO change page size to ~20
        var entity = examRepositoryOther.findFiltered(
                getStatusFilter(status),
                getTypeFilter(type),
                getSuccessFilter(successful),
                mark,
                pageable
        );
        return mapper.writeValueAsString(entity);
    }

    private @Nullable ExamRepositoryOther.FILTER_STATUS getStatusFilter(String status) {
        return switch (status) {
            case "finished" -> ExamRepositoryOther.FILTER_STATUS.FINISHED;
            case "unfinished" -> ExamRepositoryOther.FILTER_STATUS.UNFINISHED;
            default -> null;
        };
    }

    private @Nullable ExamRepositoryOther.FILTER_TYPE getTypeFilter(String status) {
        return switch (status) {
            case "nahradni_hodnoceni" -> ExamRepositoryOther.FILTER_TYPE.NAHRADNI_HODNOCENI;
            case "opravna_zkouska" -> ExamRepositoryOther.FILTER_TYPE.OPRAVNA_ZKOUSKA;
            default -> null;
        };
    }

    private @Nullable ExamRepositoryOther.FILTER_SUCCESS getSuccessFilter(String status) {
        return switch (status) {
            case "successful" -> ExamRepositoryOther.FILTER_SUCCESS.SUCCESSFUL;
            case "failed" -> ExamRepositoryOther.FILTER_SUCCESS.FAILED;
            default -> null;
        };
    }
}
