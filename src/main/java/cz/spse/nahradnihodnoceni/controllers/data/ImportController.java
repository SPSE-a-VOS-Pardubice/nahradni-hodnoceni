package cz.spse.nahradnihodnoceni.controllers.data;

import com.opencsv.bean.CsvToBean;
import com.opencsv.bean.CsvToBeanBuilder;
import cz.spse.nahradnihodnoceni.models.ImportEntry;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.RestController;
import org.springframework.web.multipart.MultipartFile;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.Reader;
import java.util.List;

@RestController
public class ImportController {

    @PostMapping("/upload-csv-file")
    public List<ImportEntry> uploadCSVFile(@RequestParam("file") MultipartFile file) throws IOException {

        // validate file
        if (file.isEmpty()) {
            return null;
        }

        Reader reader = new BufferedReader(new InputStreamReader(file.getInputStream()));

        // create csv bean reader
        CsvToBean<ImportEntry> csvToBean = new CsvToBeanBuilder<ImportEntry>(reader)
                .withType(ImportEntry.class)
                .withIgnoreLeadingWhiteSpace(true)
                .build();

        // convert `CsvToBean` object to list of users
        List<ImportEntry> entries = csvToBean.parse();

        // TODO: save users in DB?

        return entries;
    }
}
