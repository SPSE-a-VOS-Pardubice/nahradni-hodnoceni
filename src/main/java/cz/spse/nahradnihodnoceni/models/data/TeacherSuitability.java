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

    @ManyToOne(optional = false, fetch = FetchType.LAZY)
    private Subject subject;

    @ManyToOne(optional = false, fetch = FetchType.LAZY)
    private Teacher teacher;

    @Column(nullable = false)
    private int suitability = 0;
}
