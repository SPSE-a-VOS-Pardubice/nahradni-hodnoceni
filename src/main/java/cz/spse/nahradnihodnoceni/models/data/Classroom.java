package cz.spse.nahradnihodnoceni.models.data;

import java.util.Set;

import jakarta.persistence.*;
import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;

@Entity
@AllArgsConstructor
@NoArgsConstructor
@Data
public class Classroom {
    @Id
    @GeneratedValue(strategy = GenerationType.AUTO)
    private Long id;

    private boolean available = true;

    @Column(unique=true)
    private String label;

    @OneToMany(mappedBy="classroom")
    private Set<Trait> traits;
}
