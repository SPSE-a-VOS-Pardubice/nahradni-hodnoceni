package com.example.nahradnihodnoceni.repositories;

import org.springframework.data.mongodb.repository.MongoRepository;

import com.example.nahradnihodnoceni.models.Teacher;

public interface TeacherRepository extends MongoRepository<Teacher, String> {

}
