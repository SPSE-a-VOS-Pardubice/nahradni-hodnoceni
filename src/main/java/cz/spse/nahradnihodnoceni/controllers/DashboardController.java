package cz.spse.nahradnihodnoceni.controllers;

import com.fasterxml.jackson.core.JsonProcessingException;
import com.fasterxml.jackson.databind.ObjectMapper;
import cz.spse.nahradnihodnoceni.helpers.MapperHelper;
import cz.spse.nahradnihodnoceni.models.DashboardStats;
import cz.spse.nahradnihodnoceni.repositories.ExamRepository;
import cz.spse.nahradnihodnoceni.repositories.ExamRepositoryOther;
import org.springframework.beans.factory.annotation.Autowired;
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
    public String filteredExams(@RequestParam(defaultValue = "") String status, @RequestParam(defaultValue = "") String type, @RequestParam(defaultValue = "") String success, @RequestParam(defaultValue = "") String mark, @RequestParam(defaultValue = "") String form, @RequestParam(defaultValue = "") String sortBy, @RequestParam(defaultValue = "false") String reverse, @PathVariable() Integer page) throws JsonProcessingException {

        // TODO add paging

        var entity = examRepositoryOther.findFiltered(
                getStatusFilter(status),
                getTypeFilter(type),
                getSuccessFilter(success),
                mark,
                getSort(sortBy),
                getBoolean(reverse)
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

    private @Nullable ExamRepositoryOther.FILTER_TYPE getTypeFilter(String type) {
        return switch (type) {
            case "nahradni_hodnoceni" -> ExamRepositoryOther.FILTER_TYPE.NAHRADNI_HODNOCENI;
            case "opravna_zkouska" -> ExamRepositoryOther.FILTER_TYPE.OPRAVNA_ZKOUSKA;
            default -> null;
        };
    }

    private @Nullable ExamRepositoryOther.FILTER_SUCCESS getSuccessFilter(String success) {
        return switch (success) {
            case "successful" -> ExamRepositoryOther.FILTER_SUCCESS.SUCCESSFUL;
            case "failed" -> ExamRepositoryOther.FILTER_SUCCESS.FAILED;
            default -> null;
        };
    }

    private @Nullable ExamRepositoryOther.SORT_BY getSort(String sort) {
        return switch (sort) {
            case "student" -> ExamRepositoryOther.SORT_BY.STUDENT;
            case "teacher" -> ExamRepositoryOther.SORT_BY.TEACHER;
            case "class" -> ExamRepositoryOther.SORT_BY.CLASS;
            case "mark" -> ExamRepositoryOther.SORT_BY.MARK;
            default -> null;
        };
    }

    private @Nullable Boolean getBoolean(String bool) {
        return switch (bool) {
            case "1", "true" -> Boolean.TRUE;
            case "0", "false" -> Boolean.FALSE;
            default -> null;
        };
    }
}
