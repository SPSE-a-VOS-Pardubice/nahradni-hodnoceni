package cz.spse.nahradnihodnoceni.controllers.data;

import cz.spse.nahradnihodnoceni.controllers.DataController;
import cz.spse.nahradnihodnoceni.models.data.Trait;
import cz.spse.nahradnihodnoceni.repositories.TraitRepository;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;

@RestController
@RequestMapping("/api/1/data/trait")
public class TraitController extends DataController<Trait, TraitRepository> {}