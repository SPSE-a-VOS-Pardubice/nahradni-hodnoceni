package com.example.nahradnihodnoceni.models;

import org.springframework.data.annotation.Id;
import org.springframework.data.mongodb.core.index.Indexed;
import org.springframework.data.mongodb.core.mapping.Document;

import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;

@Document(collection = "traits")
@AllArgsConstructor
@NoArgsConstructor
@Data
public class Trait implements SafetyRemovable {
    @Id
    private String id;
    @Indexed(unique = true)
    private String name;
    private boolean available = true;
}
