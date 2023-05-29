package com.example.nahradnihodnoceni.models;

import java.time.LocalDateTime;

import org.springframework.data.annotation.Id;
import org.springframework.data.mongodb.core.mapping.DBRef;
import org.springframework.data.mongodb.core.mapping.Document;

import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;

@Document(collection = "exams")
@AllArgsConstructor
@NoArgsConstructor
@Data
public class Exam implements SafetyRemovable {
    @Id
    private String id;
    @DBRef
    private Student student;
    @DBRef
    private Subject subject;
    @DBRef
    private Classroom classroom;
    @DBRef
    private Teacher chairman;
    @DBRef
    private Teacher class_teacher;
    @DBRef
    private Teacher examiner;
    private LocalDateTime time;
    private String originalMark;
    private String finalMark;
    private boolean available = true;
}
