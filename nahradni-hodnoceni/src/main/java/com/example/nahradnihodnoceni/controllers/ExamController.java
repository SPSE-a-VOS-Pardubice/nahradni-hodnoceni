package com.example.nahradnihodnoceni.controllers;

import java.util.List;
import java.util.NoSuchElementException;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.DeleteMapping;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.PutMapping;
import org.springframework.web.bind.annotation.RequestBody;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.RestController;

import com.example.nahradnihodnoceni.generickService.GService;
import com.example.nahradnihodnoceni.models.Exam;
import com.example.nahradnihodnoceni.generickService.DuplicateKeyException;
import com.example.nahradnihodnoceni.generickService.ValidationException;

@RestController
@RequestMapping("/api/examps")
public class ExamController {

    @Autowired
    private GService genecirService;
    private Exam type = new Exam();

    @GetMapping("/{id}")
    public ResponseEntity<Exam> get(@PathVariable String id) {
        try {
            Exam entity = genecirService.read(type, id);

            if (entity == null) {
                throw new NoSuchElementException();
            }

            return new ResponseEntity<>(entity, HttpStatus.OK);
        } catch (NoSuchElementException e) {
            return new ResponseEntity<>(HttpStatus.NOT_FOUND);
        } catch (Exception e) {
            e.printStackTrace();
            return new ResponseEntity<>(HttpStatus.INTERNAL_SERVER_ERROR);
        }
    }

    @GetMapping("/")
    public ResponseEntity<List<Exam>> getAll() {
        try {
            List<Exam> entities = genecirService.readAll(type);

            return new ResponseEntity<List<Exam>>(entities, HttpStatus.OK);
        } catch (Exception e) {
            e.printStackTrace();
            return new ResponseEntity<>(HttpStatus.INTERNAL_SERVER_ERROR);
        }
    }

    @PostMapping("/")
    public ResponseEntity<Exam> post(@RequestBody Exam entity) {
        try {
            Exam e = genecirService.create(entity);

            return new ResponseEntity<>(e, HttpStatus.OK);
        } catch (ValidationException | DuplicateKeyException e) {
            return new ResponseEntity<>(HttpStatus.BAD_REQUEST);
        } catch (Exception e) {
            e.printStackTrace();
            return new ResponseEntity<>(HttpStatus.INTERNAL_SERVER_ERROR);
        }
    }

    @PutMapping("/{id}")
    public ResponseEntity<Exam> put(@PathVariable String id, @RequestBody Exam entity) {
        try {
            Exam e = genecirService.update(entity, id);

            return new ResponseEntity<>(e, HttpStatus.OK);
        } catch (NoSuchElementException e) {
            return new ResponseEntity<>(HttpStatus.NOT_FOUND);
        } catch (ValidationException | DuplicateKeyException e) {
            return new ResponseEntity<>(HttpStatus.BAD_REQUEST);
        } catch (Exception e) {
            e.printStackTrace();
            return new ResponseEntity<>(HttpStatus.INTERNAL_SERVER_ERROR);
        }
    }

    @DeleteMapping("/{id}")
    public ResponseEntity<Exam> delete(@PathVariable String id) {
        try {
            Exam entity = genecirService.delete(type, id);

            return new ResponseEntity<Exam>(entity, HttpStatus.OK);
        } catch (NoSuchElementException e) {
            return new ResponseEntity<>(HttpStatus.NOT_FOUND);
        } catch (Exception e) {
            e.printStackTrace();
            return new ResponseEntity<>(HttpStatus.INTERNAL_SERVER_ERROR);
        }
    }
}
