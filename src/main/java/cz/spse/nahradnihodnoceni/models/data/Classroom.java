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
public class Classroom {
    @Id
    @GeneratedValue(strategy = GenerationType.AUTO)
    private Long id;

    private boolean available = true;

    @Column(nullable = false, unique = true)
    private String label;

    @OneToMany(mappedBy="classroom")
    private Set<Trait> traits;
}
