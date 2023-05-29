package com.example.nahradnihodnoceni.generickService;

import java.util.List;
import java.util.NoSuchElementException;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

import com.example.nahradnihodnoceni.models.SafetyRemovable;

@Service
public class GService {

    @Autowired
    private GServiceRepositories repositories;

    public <E extends SafetyRemovable> E create(E data) throws ValidationException, DuplicateKeyException {
        repositories.validate(data);

        E e = repositories.getRepository(data).save(data);
        return e;
    }

    public <E extends SafetyRemovable, ID> E read(E type, ID id) throws NoSuchElementException {
        E e = repositories.getRepository(type).findById(id).get();

        if (e == null) {
            throw new NoSuchElementException();
        }

        return e;
    }

    public <E extends SafetyRemovable> List<E> readAll(E type) {
        List<E> entities = repositories.getRepository(type).findAll();
        return entities;
    }

    public <E extends SafetyRemovable, ID> E update(E data, ID id)
            throws ValidationException, DuplicateKeyException, NoSuchElementException {

        E e = repositories.getRepository(data).findById(id).get();

        if (e == null) {
            throw new NoSuchElementException();
        }

        repositories.validate(data);

        E updated = repositories.getRepository(data).save(data);
        return updated;
    }

    public <E extends SafetyRemovable, ID> E delete(E type, ID id) throws NoSuchElementException {
        E e = repositories.getRepository(type).findById(id).get();

        if (e == null) {
            throw new NoSuchElementException();
        }

        e.setAvailable(false);

        repositories.getRepository(type).save(e);
        return e;
    }
}
