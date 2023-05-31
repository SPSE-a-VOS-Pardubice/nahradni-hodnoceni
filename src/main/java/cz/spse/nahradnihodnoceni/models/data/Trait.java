package cz.spse.nahradnihodnoceni.models.data;

import jakarta.persistence.*;
import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;

@Entity
@AllArgsConstructor
@NoArgsConstructor
@Data
@Table(uniqueConstraints = { @UniqueConstraint(columnNames = { "classroom_id", "subject_id" }) })
public class Trait {
    @Id
    @GeneratedValue(strategy = GenerationType.AUTO)
    private Long id;

    @ManyToOne
    private Classroom classroom;

    @ManyToOne
    private Subject subject;

    private boolean available = true;

    @Column(unique=true)
    private String name;
}
