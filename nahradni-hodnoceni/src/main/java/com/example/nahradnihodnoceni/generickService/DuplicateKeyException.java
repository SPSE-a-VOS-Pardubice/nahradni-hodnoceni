package com.example.nahradnihodnoceni.generickService;

public class DuplicateKeyException extends Exception {
    public DuplicateKeyException(String message) {
        super(message);
    }
}
