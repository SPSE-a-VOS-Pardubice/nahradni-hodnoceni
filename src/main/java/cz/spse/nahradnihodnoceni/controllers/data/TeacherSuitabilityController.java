package cz.spse.nahradnihodnoceni.controllers.data;

import cz.spse.nahradnihodnoceni.controllers.DataController;
import cz.spse.nahradnihodnoceni.models.data.TeacherSuitability;
import cz.spse.nahradnihodnoceni.repositories.TeacherSuitabilityRepository;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;

@RestController
@RequestMapping("/api/1/data/teacher-suitability")
public class TeacherSuitabilityController extends DataController<TeacherSuitability, TeacherSuitabilityRepository> {}
