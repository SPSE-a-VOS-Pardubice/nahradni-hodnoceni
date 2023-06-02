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

    public static final String[] marks = {"1","2","3","4","5","N"};

    @Id
    @GeneratedValue(strategy = GenerationType.AUTO)
    private Long id;

    private boolean available = true;

    @ManyToOne(optional = false)
    private Student student;

    @ManyToOne(optional = false)
    private Subject subject;

    @ManyToOne
    private Classroom classroom;

    @ManyToOne
    private Teacher chairman;

    @ManyToOne
    private Teacher class_teacher;

    @ManyToOne(optional = false)
    private Teacher examiner;

    @Temporal(TemporalType.TIMESTAMP)
    private Date time;

    @Column(nullable = false)
    private String originalMark;

    private String finalMark;
}
