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
public class Teacher {
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @Builder.Default
    private boolean available = true;

    @Builder.Default
    private boolean archived = false;

    @Column(nullable = false)
    private String name;

    @Column(nullable = false)
    private String surname;

    @Column(nullable = false)
    private String prefix;

    @Column(nullable = false)
    private String suffix;

    @OneToMany(mappedBy="teacher")
    @Builder.Default
    private Set<TeacherSuitability> suitability = new HashSet<>();
}
