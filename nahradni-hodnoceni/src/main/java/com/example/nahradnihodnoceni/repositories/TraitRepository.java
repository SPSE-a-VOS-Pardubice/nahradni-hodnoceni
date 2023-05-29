package com.example.nahradnihodnoceni.repositories;

import org.springframework.data.mongodb.repository.MongoRepository;

import com.example.nahradnihodnoceni.models.Trait;

public interface TraitRepository extends MongoRepository<Trait, String> {

}
