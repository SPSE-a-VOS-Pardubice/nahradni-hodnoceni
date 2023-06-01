package cz.spse.nahradnihodnoceni.models.data;

import jakarta.persistence.*;
import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;

import java.util.Set;

@Entity
@AllArgsConstructor
@NoArgsConstructor
@Data
public class Subject {
    @Id
    @GeneratedValue(strategy = GenerationType.AUTO)
    private Long id;

    private boolean available = true;

    @Column(nullable = false)
    private String name;

    @Column(nullable = false)
    private String abbreviation;

    @OneToMany(mappedBy="subject")
    private Set<Trait> traits;
}
