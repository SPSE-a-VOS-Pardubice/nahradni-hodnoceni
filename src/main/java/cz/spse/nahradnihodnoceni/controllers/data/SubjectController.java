package cz.spse.nahradnihodnoceni.controllers.data;

import cz.spse.nahradnihodnoceni.controllers.DataController;
import cz.spse.nahradnihodnoceni.models.data.Subject;
import cz.spse.nahradnihodnoceni.repositories.SubjectRepository;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;

@RestController
@RequestMapping("/api/1/data/subject")
public class SubjectController extends DataController<Subject, SubjectRepository> {}
