package com.example.nahradnihodnoceni.models;

import java.util.Map;
import java.util.TreeMap;

import org.springframework.data.annotation.Id;
import org.springframework.data.mongodb.core.mapping.Document;

import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;

@Document(collection = "subjects")
@AllArgsConstructor
@NoArgsConstructor
@Data
public class Subject implements SafetyRemovable {
    @Id
    private String id;
    private String name;
    private String abbereviation;
    private Map<String, Trait> traits = new TreeMap<>();
    private boolean available = true;
}
