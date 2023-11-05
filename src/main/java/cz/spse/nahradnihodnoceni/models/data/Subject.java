package cz.spse.nahradnihodnoceni.models.data;

import jakarta.persistence.*;
import lombok.*;

import java.util.Set;

@Entity
@AllArgsConstructor
@NoArgsConstructor
@Builder
@Getter
@Setter
public class Subject {
    @Id
    @GeneratedValue(strategy = GenerationType.AUTO)
    private Long id;

    @Builder.Default
    private boolean available = true;

    @Column(nullable = false)
    private String name;

    @Column(nullable = false)
    private String abbreviation;

    @OneToMany(mappedBy="subject")
    private Set<Trait> traits;
}
