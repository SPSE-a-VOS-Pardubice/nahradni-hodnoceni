package com.example.nahradnihodnoceni.generickService;

import com.example.nahradnihodnoceni.generickService.DuplicateKeyException;
import com.example.nahradnihodnoceni.generickService.ValidationException;

@FunctionalInterface
public interface Validator<E> {
    void validate(E data) throws ValidationException, DuplicateKeyException;
}
