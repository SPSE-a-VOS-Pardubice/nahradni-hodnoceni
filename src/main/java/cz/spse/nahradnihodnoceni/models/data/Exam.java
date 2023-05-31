package cz.spse.nahradnihodnoceni.models.data;

import java.time.LocalDateTime;

import jakarta.persistence.*;
import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;

@Entity
@AllArgsConstructor
@NoArgsConstructor
@Data
public class Exam {
    @Id
    @GeneratedValue(strategy = GenerationType.AUTO)
    private Long id;

    private boolean available = true;

    @ManyToOne
    private Student student;

    @ManyToOne
    private Subject subject;

    @ManyToOne
    private Classroom classroom;

    @ManyToOne
    private Teacher chairman;

    @ManyToOne
    private Teacher class_teacher;

    @ManyToOne
    private Teacher examiner;

    private LocalDateTime time;

    private String originalMark;

    private String finalMark;
}
