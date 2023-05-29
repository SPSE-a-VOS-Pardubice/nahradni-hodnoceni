package com.example.nahradnihodnoceni.models;

import org.springframework.data.annotation.Id;
import org.springframework.data.mongodb.core.mapping.DBRef;
import org.springframework.data.mongodb.core.mapping.Document;

import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;

@Document(collection = "classes")
@AllArgsConstructor
@NoArgsConstructor
@Data
public class _Class implements SafetyRemovable {
    @Id
    private String id;
    private int year;
    private String label;
    @DBRef
    private Teacher Teacher;
    private boolean available = true;
}
