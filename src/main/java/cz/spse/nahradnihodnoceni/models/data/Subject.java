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
public class Subject {
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private int id;

    @Builder.Default
    private boolean available = true;

    @Column(nullable = false)
    private String name;

    @Column(nullable = false)
    private String abbreviation;

    @ManyToMany(mappedBy="subjects")
    @Builder.Default
    private Set<Trait> traits = new HashSet<>();

    @OneToMany(mappedBy="subject")
    @Builder.Default
    private Set<TeacherSuitability> suitability = new HashSet<>();
}
