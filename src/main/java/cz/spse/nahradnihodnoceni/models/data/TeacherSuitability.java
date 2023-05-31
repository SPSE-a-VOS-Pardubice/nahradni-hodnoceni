package cz.spse.nahradnihodnoceni.models.data;

import jakarta.persistence.*;
import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;

@Entity
@AllArgsConstructor
@NoArgsConstructor
@Data
@Table(uniqueConstraints = { @UniqueConstraint(columnNames = { "subject_id", "teacher_id", "suitability" }) })
public class TeacherSuitability {
    @Id
    @GeneratedValue(strategy = GenerationType.AUTO)
    private Long id; // TODO

    @ManyToOne(fetch = FetchType.LAZY)
    private Subject subject;

    @ManyToOne(fetch = FetchType.LAZY)
    private Teacher teacher;

    private int suitability;
}
