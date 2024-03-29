package cz.spse.nahradnihodnoceni.models.data;

import jakarta.persistence.*;
import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;

@Entity
@AllArgsConstructor
@NoArgsConstructor
@Data
public class _Class {
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    private boolean available = true;

    private int year;

    @Column(nullable = false)
    private String label;

    @ManyToOne
    private Teacher teacher;
}
