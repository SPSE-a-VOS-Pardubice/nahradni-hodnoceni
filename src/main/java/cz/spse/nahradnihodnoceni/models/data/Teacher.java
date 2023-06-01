package cz.spse.nahradnihodnoceni.models.data;

import jakarta.persistence.*;
import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;
import org.springframework.lang.NonNull;

import java.util.HashSet;
import java.util.Set;

@Entity
@AllArgsConstructor
@NoArgsConstructor
@Data
public class Teacher {
    @Id
    @GeneratedValue(strategy = GenerationType.AUTO)
    private Long id;

    private boolean available = true;

    @NonNull
    private String name;

    @NonNull
    private String surname;

    @NonNull
    private String prefix;

    @NonNull
    private String suffix;

    @OneToMany(mappedBy="teacher", fetch = FetchType.EAGER, cascade = CascadeType.ALL, orphanRemoval = true)
    private Set<TeacherSuitability> suitability = new HashSet<>();
}
