package cz.spse.nahradnihodnoceni.controllers;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.data.repository.CrudRepository;
import org.springframework.http.MediaType;
import org.springframework.web.bind.annotation.*;

public abstract class DataController<E, R extends CrudRepository<E, Long>> {
    @Autowired
    private R repository;

    @GetMapping(value = "/{id}", produces = MediaType.APPLICATION_JSON_VALUE)
    public E get(@PathVariable("id") long id) {
        var e = repository.findById(id).orElseThrow();
        return e;
    }

    @PostMapping(value = "", produces = MediaType.APPLICATION_JSON_VALUE)
    public E post(@RequestBody E entity) {
        return repository.save(entity);
    }
}
