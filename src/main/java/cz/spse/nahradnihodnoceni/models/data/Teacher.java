package cz.spse.nahradnihodnoceni.models.data;

import java.util.List;

import jakarta.persistence.*;
import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;

@Entity
@AllArgsConstructor
@NoArgsConstructor
@Data
public class Teacher {
    @Id
    @GeneratedValue(strategy = GenerationType.AUTO)
    private Long id;

    private boolean available;

    private String name;

    private String surname;

    private String prefix;

    private String suffix;

    @OneToMany(mappedBy="teacher")
    private List<TeacherSuitability> suitability;
}
