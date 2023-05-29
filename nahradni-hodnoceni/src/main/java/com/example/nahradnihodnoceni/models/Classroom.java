package com.example.nahradnihodnoceni.models;

import java.util.Map;
import java.util.TreeMap;

import org.springframework.data.annotation.Id;
import org.springframework.data.mongodb.core.index.Indexed;
import org.springframework.data.mongodb.core.mapping.Document;

import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;

@Document(collection = "classrooms")
@AllArgsConstructor
@NoArgsConstructor
@Data
public class Classroom implements SafetyRemovable {
    @Id
    private String id;
    @Indexed(unique = true)
    private String label;
    private Map<String, Trait> traits = new TreeMap<>();
    private boolean available = true;
}
