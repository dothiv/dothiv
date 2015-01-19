package main

//
// This file creates the
// src/Dothiv/BaseWebsiteBundle/Resources/public/data/countries-(de|en).json
// files from
// src/Dothiv/BaseWebsiteBundle/Resources/public/data/countries.csv
//
// Usage: go run app/Resources/countries2json.go
//

import (
	"encoding/csv"
	"encoding/json"
	"fmt"
	"io"
	"os"
)

func writeJson(lang string, index int) (err error) {
	source, err := os.Open("./src/Dothiv/BaseWebsiteBundle/Resources/public/data/countries.csv")
	if err != nil {
		return
	}
	defer source.Close()

	countries := make([]interface{}, 0)

	csvReader := csv.NewReader(source)
	// Skip header
	csvReader.Read()
	for {
		line, csvErr := csvReader.Read()
		if csvErr == io.EOF {
			break
		}
		if csvErr != nil {
			err = csvErr
			return
		}

		EU := 0
		label := ""
		if line[index] == line[0] {
			label = line[0]
		} else {
			label = fmt.Sprintf("%s (%s)", line[index], line[0])
		}

		if line[3] == "1" {
			EU = 1
		}
		country := []interface{}{}
		country = append(country, label)
		country = append(country, EU)
		countries = append(countries, country)
	}

	j, err := json.Marshal(countries)
	if err != nil {
		return
	}

	jsonFile, err := os.OpenFile("./src/Dothiv/BaseWebsiteBundle/Resources/public/data/countries-"+lang+".json", os.O_RDWR|os.O_TRUNC|os.O_CREATE, 0644)
	if err != nil {
		return
	}
	defer jsonFile.Close()
	jsonFile.Write(j)
	os.Stdout.WriteString(jsonFile.Name())
	os.Stdout.WriteString("\n")
	return
}

func main() {

	err := writeJson("en", 1)
	if err != nil {
		os.Stderr.WriteString(err.Error())
		os.Exit(1)
	}

	err = writeJson("de", 2)
	if err != nil {
		os.Stderr.WriteString(err.Error())
		os.Exit(1)
	}
}
