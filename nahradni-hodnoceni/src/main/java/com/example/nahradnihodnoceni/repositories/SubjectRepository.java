package com.example.nahradnihodnoceni.repositories;

import org.springframework.data.mongodb.repository.MongoRepository;

import com.example.nahradnihodnoceni.models.Subject;

public interface SubjectRepository extends MongoRepository<Subject, String> {

}
