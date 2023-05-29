package com.example.nahradnihodnoceni.models;

import org.springframework.data.annotation.Id;
import org.springframework.data.mongodb.core.mapping.DBRef;
import org.springframework.data.mongodb.core.mapping.Document;

import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;

@Document(collection = "students")
@AllArgsConstructor
@NoArgsConstructor
@Data
public class Student implements SafetyRemovable {
    @Id
    private String id;
    private String name;
    private String surname;
    @DBRef
    private _Class _class;
    private boolean available = true;
}
