package cz.spse.nahradnihodnoceni.controllers;

import com.fasterxml.jackson.core.JsonProcessingException;
import com.fasterxml.jackson.databind.ObjectMapper;
import cz.spse.nahradnihodnoceni.helpers.MapperHelper;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.data.repository.CrudRepository;
import org.springframework.http.MediaType;
import org.springframework.web.bind.annotation.*;

public abstract class DataController<E, R extends CrudRepository<E, Long>> {
    @Autowired
    private R repository;

    private final ObjectMapper mapper = MapperHelper.createMapper();

    @GetMapping(value = "/{id}", produces = MediaType.APPLICATION_JSON_VALUE)
    public String get(@PathVariable("id") long id) throws JsonProcessingException {
        var entity = repository.findById(id).orElseThrow();
        return mapper.writeValueAsString(entity);
    }

    @GetMapping(value = "/list", produces = MediaType.APPLICATION_JSON_VALUE)
    public String list() throws JsonProcessingException {
        var entity = repository.findAll();
        return mapper.writeValueAsString(entity);
    }

    @PostMapping(value = "", produces = MediaType.APPLICATION_JSON_VALUE)
    public String post(@RequestBody E entity) throws JsonProcessingException {
        var newEntity = repository.save(entity);
        return mapper.writeValueAsString(newEntity);
    }

    @DeleteMapping(value = "/{id}")
    public void delete(@PathVariable("id") long id) {
        repository.deleteById(id);
    }
}
