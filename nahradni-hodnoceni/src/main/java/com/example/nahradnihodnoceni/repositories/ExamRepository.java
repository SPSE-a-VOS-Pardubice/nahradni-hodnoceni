package com.example.nahradnihodnoceni.repositories;

import org.springframework.data.mongodb.repository.MongoRepository;

import com.example.nahradnihodnoceni.models.Exam;

public interface ExamRepository extends MongoRepository<Exam, String> {

}
