package cz.spse.nahradnihodnoceni.models.data;

import jakarta.persistence.*;
import lombok.*;

import java.util.Date;

@Entity
@AllArgsConstructor
@NoArgsConstructor
@Builder
@Getter
@Setter
public class Exam {

    public static final String[] marks = {"1","2","3","4","5","N"};

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @Builder.Default
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
    private Teacher classTeacher;

    @ManyToOne(optional = false)
    private Teacher examiner;

    @Temporal(TemporalType.TIMESTAMP)
    private Date time;

    private int year;
    private int period;

    @Column(nullable = false)
    private String originalMark;

    private String finalMark;
}
