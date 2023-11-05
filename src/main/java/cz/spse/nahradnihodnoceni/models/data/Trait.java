package cz.spse.nahradnihodnoceni.models.data;

import jakarta.persistence.*;
import lombok.*;

import java.util.HashSet;
import java.util.Set;

@Entity
@AllArgsConstructor
@NoArgsConstructor
@Builder
@Getter
@Setter
public class Trait {
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @ManyToMany()
    @JoinTable(
            name = "Trait_Classroom",
            joinColumns = { @JoinColumn(name = "trait_id") },
            inverseJoinColumns = { @JoinColumn(name = "classroom_id") }
    )
    @Builder.Default
    private Set<Classroom> classrooms = new HashSet<>();

    @ManyToMany()
    @JoinTable(
            name = "Trait_Subject",
            joinColumns = { @JoinColumn(name = "trait_id") },
            inverseJoinColumns = { @JoinColumn(name = "subject_id") }
    )
    @Builder.Default
    private Set<Subject> subjects = new HashSet<>();

    @Builder.Default
    private boolean available = true;

    @Column(unique=true)
    private String name;
}
