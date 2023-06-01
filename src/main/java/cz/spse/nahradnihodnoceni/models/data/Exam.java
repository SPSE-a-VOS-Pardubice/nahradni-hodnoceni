package cz.spse.nahradnihodnoceni.models.data;

import jakarta.persistence.*;
import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;

import java.util.Date;

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

    @Temporal(TemporalType.TIMESTAMP)
    private Date time;

    private String originalMark;

    private String finalMark;
}
