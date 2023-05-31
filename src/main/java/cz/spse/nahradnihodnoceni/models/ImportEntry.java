package cz.spse.nahradnihodnoceni.models;

import com.opencsv.bean.CsvBindByPosition;
import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;

@AllArgsConstructor
@NoArgsConstructor
@Data
public class ImportEntry {

    @CsvBindByPosition(position = 0)
    private String trida;

    @CsvBindByPosition(position = 1)
    private String jmeno;

    @CsvBindByPosition(position = 2)
    private String prijmeni;

    @CsvBindByPosition(position = 3)
    private String predmet;

    @CsvBindByPosition(position = 4)
    private String znamka;

    @CsvBindByPosition(position = 5)
    private String zkousejici;
}
