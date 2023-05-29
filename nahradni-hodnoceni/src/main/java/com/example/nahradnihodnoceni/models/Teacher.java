package com.example.nahradnihodnoceni.models;

import java.util.Map;
import java.util.TreeMap;

import org.springframework.data.annotation.Id;
import org.springframework.data.mongodb.core.mapping.Document;

import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;

@Document(collection = "teachers")
@AllArgsConstructor
@NoArgsConstructor
@Data
public class Teacher implements SafetyRemovable {
    @Id
    private String id;
    private String name;
    private String surname;
    private String prefix;
    private String sufix;
    private Map<String, TeacherSuitability> suitability = new TreeMap<>();
    private boolean available = true;
}
