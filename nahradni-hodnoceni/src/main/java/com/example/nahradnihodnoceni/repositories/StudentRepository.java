package com.example.nahradnihodnoceni.repositories;

import org.springframework.data.mongodb.repository.MongoRepository;

import com.example.nahradnihodnoceni.models.Student;

public interface StudentRepository extends MongoRepository<Student, String> {

}
