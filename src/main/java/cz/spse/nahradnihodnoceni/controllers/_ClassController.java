package cz.spse.nahradnihodnoceni.controllers;

import cz.spse.nahradnihodnoceni.models.data._Class;
import cz.spse.nahradnihodnoceni.repositories._ClassRepository;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;

@RestController
@RequestMapping("/api/1/data/class")
public class _ClassController extends DataController<_Class, _ClassRepository> {}