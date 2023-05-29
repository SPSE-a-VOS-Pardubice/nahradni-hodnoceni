package com.example.nahradnihodnoceni.repositories;

import org.springframework.data.mongodb.repository.MongoRepository;

import com.example.nahradnihodnoceni.models.Classroom;

public interface ClassroomRepository extends MongoRepository<Classroom, String> {

}
