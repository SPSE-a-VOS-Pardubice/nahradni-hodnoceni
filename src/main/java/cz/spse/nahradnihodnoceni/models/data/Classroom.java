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
public class Classroom {
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @Builder.Default
    private boolean available = true;

    @Builder.Default
    private boolean archived = false;

    @Column(nullable = false, unique = true)
    private String label;

    @ManyToMany(mappedBy="classrooms")
    @Builder.Default
    private Set<Trait> traits = new HashSet<>();
}
