package cz.spse.nahradnihodnoceni.models.data;

import jakarta.persistence.*;
import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;

import java.time.LocalDate;

@Entity
@AllArgsConstructor
@NoArgsConstructor
@Data
public class _Class {
    @Id
    @GeneratedValue(strategy = GenerationType.AUTO)
    private Long id;

    private boolean available = true;

    private int year;

    @Column(nullable = false)
    private String label;

    @ManyToOne
    private Teacher teacher;

    public int countYear() {
        return  Math.max(1, LocalDate.now().getYear() - getYear());
    }
}
